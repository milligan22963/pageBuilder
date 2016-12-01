/**
 * Inheritance
 * 
 * see: http://phrogz.net/js/classes/OOPinJS2.html
 */

/**
 * Hack in support for Function.name for browsers that don't support it.
 * IE, I'm looking at you.
 * http://matt.scharley.me/2012/03/monkey-patch-name-ie.html
**/
if (Function.prototype.name === undefined && Object.defineProperty !== undefined) {
    Object.defineProperty(Function.prototype, 'name', {
        get: function() {
            var funcNameRegex = /function\s([^(]{1,})\(/;
            var results = (funcNameRegex).exec((this).toString());
            return (results && results.length > 1) ? results[1].trim() : "";
        },
        set: function(value) {}
    });
}

Function.prototype.inheritsFrom = function(parentObj)
{
    // normal object i.e. function
    if (parentObj.constructor == Function)
    {
        this.prototype = new parentObj;
        this.prototype.parent.push(parentObj.prototype);
    }
    else // "pure virtual"
    {
        this.prototype = parentObj;
        this.prototype.parent.push(parentObj);
    }
    this.prototype.constructor = this;
    this.prototype[this.prototype.constructor.name] = this.prototype.parent.length - 1;

    return this;
}