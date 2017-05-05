/* jQuery PHP-Like Serialize
 * 
 * Author: Skazzino
 */
(function($) {
    /* Classes to ignore when serializing!
    * 
    * @type Array
    */
    var ignore = [
        '$',
        'jQuery',
        'HTMLElement'
    ];
    
    /* serialize and unserialize functions converted from Locutus NodeJS Library into Client Javascript.
     * Also added support for functions serializing/unserializing.
     * 
     */
    var PhpLikeSerializer = function(params) {
        var generic = params.generic;
        /* UID Key almost impossible to match! Unless you want to hack this :) */
        var UIDkey = '@__1234567890abcdefghijklmnoprqstuvwyzUIDkey#__@';
        
        /* Zero length codes of UTF8.
         *
         * @type Array
         */
        var zeroCodes = [
            338,
            339,
            352,
            353,
            376,
            402,
            8211,
            8212,
            8216,
            8217,
            8218,
            8220,
            8221,
            8222,
            8224,
            8225,
            8226,
            8230,
            8240,
            8364,
            8482
        ];

        /* Calculates the UTF8 size for a character.
         * The encoding wasn't doing the same of decoding with lengths so I had to change the code properly.
         *
         * @param {type} str
         * @returns {Number}
         */
        var _utf8Size = function (str) {
            var size = 0;
            var i = 0;
            var l = str.length;
            var code = '';
            for (i = 0; i < l; i++) {
                code = str.charCodeAt(i);
                if (code < 0x0080 || code >= 0x00A0 && code <= 0x00FF || zeroCodes.indexOf(code) !== -1) {
                    size += 1;
                } else if (code < 0x0800) {
                    size += 2;
                } else {
                    size += 3;
                }
            }

            return size;
        };

        /* Removes the UTF8 overhead from a character.
         *
         * @param {type} chr
         * @returns {Number}
         */
        var _utf8Overhead = function (chr) {
            var code = chr.charCodeAt(0);
            if (code < 0x0080 || code >= 0x00A0 && code <= 0x00FF || zeroCodes.indexOf(code) !== -1) {
                return 0;
            }
            if (code < 0x0800) {
                return 1;
            }
            return 2;
        };

        /* Returns the type of an input value (in Javascript).
         *
         * @param {type} inp
         * @returns {Array|PhpSerializer._getType.types|String}
         */
        var _getType = function (inp) {
            var match;
            var key;
            var cons;
            var types;
            var type = typeof inp;

            if (type === 'object' && !inp) {
                return 'null';
            }

            if (type === 'object') {
                if (!inp.constructor) {
                    return 'object';
                }

                cons = inp.constructor.toString();
                match = cons.match(/(\w+)\(/);
                if (match) {
                    cons = match[1].toLowerCase();
                }

                types = ['boolean', 'number', 'string', 'array'];
                for (key in types) {
                    if (cons === types[key]) {
                        type = types[key];
                        break;
                    }
                }
            }

            return type;
        };

        /* Read data from offset until a stop character.
         *
         * @param {type} data
         * @param {type} offset
         * @param {type} stopchr
         * @returns {Array}
         */
        var _readUntil = function (data, offset, stopchr) {
            var i = 2;
            var buf = [];
            var chr = data.slice(offset, offset + 1);

            while (chr !== stopchr) {
                if ((i + offset) > data.length) {
                    console.error('Error: invalid.');
                    break;
                }

                buf.push(chr);
                chr = data.slice(offset + (i - 1), offset + i);
                i += 1;
            }

            return [buf.length, buf.join('')];
        };

        /* Read data from offset until length.
         *
         * @param {type} data
         * @param {type} offset
         * @param {type} length
         * @returns {Array}
         */
        var _readChrs = function (data, offset, length) {
            var i, chr, buf;

            buf = [];
            for (i = 0; i < length; i++) {
                chr = data.slice(offset + (i - 1), offset + i);
                buf.push(chr);
                length -= _utf8Overhead(chr);
            }

            return [buf.length, buf.join('')];
        };

        /* Check if a string is base64 encoded, if so decodes it, checks if the UID is matches the parameter,
         * converts the string into a function with eval and returns the function.
         * If it's not a base64 encoded string or string is not a function, returns original data.
         * 
         * @param {type} x
         * @param {type} UID
         * @returns {window.atob}
         */
        var _convertToFunction = function (x, UID) {
            var UIDstring = '@__F' + UID + '__@';
            
            try {
                x = atob(x);
                if(x.match(UIDstring)) {
                    x = x.replace('@__F' + UID + '__@', 'function');

                    var newFunction;
                    eval('newFunction = ' + x);

                    return newFunction;
                }
                
                console.error('Error: not a valid function.');
            } catch(e) {}
            
            return x;
        };
        
        /* Checks if a constructor is defined as object attribute.
         * If so, creates a typed object using the constructor name. If not returns the original object.
         * 
         * @param {type} x
         * @param {type} UID
         * @returns {unresolved}
         */
        var _convertToTypedObject = function(x, UID) {
            if(!generic) {
                var constructorName = x['@__C-' + UID + '__@'];

                if(constructorName !== undefined)
                    eval('x = $.extend(new ' + constructorName + '(), x);');
            }
            
            return x;
        };
        
        /* Checks if there are any functions marked for serializing.
         * 
         * @param {type} x
         * @returns {Boolean}
         */
        var _needToSerializeFunctions = function(x) {
            for (var key in x) {
                if (x.hasOwnProperty(key)) {
                    var ktype = _getType(x[key]);

                    if (ktype === 'function' && key.match(/_serializable/i))
                        return true;
                }
            }
            
            return false;
        };

        /* Serializes (like PHP) a mixed value.
         * This is wrapper for _serialize private method, which is recursive and has UID extra parameter.
         * This method doesn't save passed object methods, but if you want to save an attribute which is a function,
         * you should add '_serializable' at the end of the attribute name in your object.
         * Example: myFun => myFun_serializable
         *
         * @param {type} data
         * @returns {unresolved}
         */
        this.serialize = function(mixedValue) {
            return _serialize(mixedValue, '');
        };

        /* The real serialize function.
         *
         * @param {type} mixedValue
         * @returns {String}
         */
        var _serialize = function(mixedValue, UID) {
            var val, key, okey;
            var ktype = '';
            var vals = '';
            var count = 0;

            var type = _getType(mixedValue);

            switch (type) {
                case 'function':
                    /* We get the string version of function, replace "function" keyword with an UID 
                     * and base64 encode it. */
                    var stringVersion = mixedValue.toString();
                    var newUID = (UID !== '') ? ('-' + UID) : '';
                    stringVersion = btoa(stringVersion.replace(/function ?/i, '@__F' + newUID + '__@'));
                    
                    val = 's:' + _utf8Size(stringVersion) + ':"' + stringVersion + '"';
                break;
                
                case 'boolean':
                    val = 'b:' + (mixedValue ? '1' : '0');
                break;
                
                case 'number':
                    val = (Math.round(mixedValue) === mixedValue ? 'i' : 'd') + ':' + mixedValue;
                break;
                
                case 'string':
                    val = 's:' + _utf8Size(mixedValue) + ':"' + mixedValue + '"';
                break;
                
                case 'array':
                    val = 'a';

                    for (key in mixedValue) {
                        if (mixedValue.hasOwnProperty(key)) {
                            ktype = _getType(mixedValue[key]);

                            if (ktype === 'function') {
                                /* No function will be saved in arrays. */
                                continue;
                            }

                            okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                            vals += _serialize(okey, UID) + _serialize(mixedValue[key], UID);
                            count++;
                        }
                    }
                    val += ':' + count + ':{' + vals + '}';
                break;
                
                case 'object':
                    var test = false;
                  
                    /* We check if we need to ignore this object */
                    for(var i = 0; i < ignore.length; i++) {
                        eval('test = mixedValue instanceof ' + ignore[i]);
                        if(test) break;
                    }
                    
                    if(test)
                        val = 'N';
                    else {
                        val = 'a';
                        
                        /* If there's a function to serialize or if we need to save object type, 
                         * we put a random UID in the object to improve security! */
                        if(_needToSerializeFunctions(mixedValue) || 
                           (!generic && (mixedValue.constructor.name !== '' && mixedValue.constructor.name !== 'Object'))) {
                            var newUID = Math.floor(Math.random() * 0x10000000000).toString(16);
                            mixedValue[UIDkey] = newUID;
                        }
                        
                        /* If generic flag is false, we save object type, except if it's not null or Object. */
                        if(!generic) {
                            if(mixedValue.constructor.name !== '' && mixedValue.constructor.name !== 'Object')
                                mixedValue['@__C-' + newUID + '__@'] = mixedValue.constructor.name;
                        }
                        
                        for (key in mixedValue) {
                            if (mixedValue.hasOwnProperty(key)) {
                                ktype = _getType(mixedValue[key]);

                                if (ktype === 'function') {
                                    /* Only saves a function if it's marked with _serializable */
                                    if(!key.match(/_serializable/i))
                                        continue;
                                }

                                okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                                vals += _serialize(okey, newUID) + _serialize(mixedValue[key], newUID);
                                count++;
                            }
                        }

                        val += ':' + count + ':{' + vals + '}';
                    }
                break;
                
                case 'undefined':
                default:
                    val = 'N';
                break;
            }

            if (type !== 'object' && type !== 'array') {
                val += ';';
            }

            return val;
        };

        /* Unserializes (like PHP) a serialized value.
         * This is a wrapper for _unserialize private method, which is recursive and also
         * makes extra checks on unserialized value (for function parsing).
         *
         * @param {type} data
         * @returns {unresolved}
         */
        this.unserialize = function(data) {
            var unserialized = _unserialize((data + ''), 0)[2];
            return _afterUnserialize(unserialized);
        };

        /* The real unserialize function, recursive.
         *
         * @param {type} data
         * @param {type} offset
         * @returns {Array}
         */
        var _unserialize = function (data, offset) {
            var dtype;
            var dataoffset;
            var keyandchrs;
            var keys;
            var contig;
            var length;
            var array;
            var readdata;
            var readData;
            var ccount;
            var stringlength;
            var i;
            var key;
            var kprops;
            var kchrs;
            var vprops;
            var vchrs;
            var value;
            var chrs = 0;

            var typeconvert = function (x) {
                return x;
            };

            if (!offset) {
                offset = 0;
            }

            dtype = (data.slice(offset, offset + 1)).toLowerCase();
            dataoffset = offset + 2;

            switch (dtype) {
                case 'i':
                    typeconvert = function (x) {
                        return parseInt(x, 10);
                    };

                    readData = _readUntil(data, dataoffset, ';');
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                break;
                    
                case 'b':
                    typeconvert = function (x) {
                        return parseInt(x, 10) !== 0;
                    };

                    readData = _readUntil(data, dataoffset, ';');
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                break;
                    
                case 'd':
                    typeconvert = function (x) {
                        return parseFloat(x);
                    };

                    readData = _readUntil(data, dataoffset, ';');
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                break;
                    
                case 'n':
                    readdata = null;
                break;
                    
                case 's':
                    ccount = _readUntil(data, dataoffset, ':');
                    chrs = ccount[0];
                    stringlength = ccount[1];
                    dataoffset += chrs + 2;

                    readData = _readChrs(data, dataoffset + 1, parseInt(stringlength, 10));
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 2;
                    if (chrs !== parseInt(stringlength, 10) && chrs !== readdata.length) {
                        console.error('Syntax Error: string length mismatch.');
                        break;
                    }
                break;
                    
                case 'a':
                    readdata = {};

                    keyandchrs = _readUntil(data, dataoffset, ':');
                    chrs = keyandchrs[0];
                    keys = keyandchrs[1];
                    dataoffset += chrs + 2;

                    length = parseInt(keys, 10);
                    contig = true;

                    for (i = 0; i < length; i++) {
                        kprops = _unserialize(data, dataoffset);
                        kchrs = kprops[1];
                        key = kprops[2];
                        dataoffset += kchrs;

                        vprops = _unserialize(data, dataoffset);
                        vchrs = vprops[1];
                        value = vprops[2];
                        dataoffset += vchrs;

                        if (key !== i) {
                            contig = false;
                        }

                        readdata[key] = value;
                    }

                    if (contig) {
                        array = new Array(length);
                        for (i = 0; i < length; i++) {
                            array[i] = readdata[i];
                        }

                        readdata = array;
                    }

                    dataoffset += 1;
                break;
                    
                default:
                    console.error('Syntax Error: unknown/unhandled data type(s): ' + dtype + '.');
                break;
            }

            return [dtype, dataoffset - offset, typeconvert(readdata)];
        };
        
        /* In this method we do some special checks on unserialized value.
         * If it's a string, we check if it's actually a function and convert it.
         * 
         * If it's an object we check if it's a typed object and convert it. 
         * Then we check if there are any serialized functions in the object and convert them.
         */
        var _afterUnserialize = function(data) {
            var type = _getType(data);

            switch(type) {
                case 'string':
                    return _convertToFunction(data, '');
                break;

                case 'object':
                    var UID = data[UIDkey];

                    if(UID !== undefined) {
                        data = _convertToTypedObject(data, UID);

                        for (var key in data) {
                            if (data.hasOwnProperty(key)) {
                                var ktype = _getType(data[key]);

                                if(key.match(/_serializable/i))
                                    if (ktype === 'string')
                                        data[key] = _convertToFunction(data[key], '-' + UID);
                            }
                        }

                        delete data[UIDkey];
                        delete data['@__C-' + UID + '__@'];
                    }
                break;
            }

            return data;
        };
    };
    
    /* Check if params are passed to function, if not use defaults :)
     * 
     */
    var _manageParams = function(params) {
        params = params || { generic: false };
        
        return params;
    };
    
    /* Adds a class to the ignore array.
     * 
     * @param {type} ignoreClass
     * @returns {undefined}
     */
    var addIgnoreClass = function(ignoreClass) {
        if(ignoreClass === 'Object') return;
        
        if($.inArray(ignoreClass, ignore) < 0)
            ignore.push(ignoreClass);
    };
    
    /* Plugin namespace. Here we declare our functions.
     * 
     */
    $.phpSerializer = {
        serialize: function(input, params) {
            params = _manageParams(params);

            var phpSerializer = new PhpLikeSerializer(params);
            return phpSerializer.serialize(input);
        },
        unserialize: function(input, params) {
            params = _manageParams(params);
        
            var phpSerializer = new PhpLikeSerializer(params);
            return phpSerializer.unserialize(input);
        },
        ignoreClass: function(ignoreClasses) {
            var type = typeof ignoreClasses;
            
            switch(type) {
                case 'string':
                    addIgnoreClass(ignoreClasses);
                break;
                
                case 'object':
                    var cons = ignoreClasses.constructor.toString();
                    var match = cons.match(/(\w+)\(/);
                    if (match) cons = match[1].toLowerCase();
                    
                    if(cons === 'array')
                        for(var i = 0; i < ignoreClasses.length; i++)
                            addIgnoreClass(ignoreClasses[i]);
                break;
            }
        }
    };
}(jQuery));
