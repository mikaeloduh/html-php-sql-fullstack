/*==================================================================================
 * Utils.js
 *
 * Author: Joshua Boley
 * 
 * Personal utility function library.
 *
 *================================================================================*/

var
    /* hasMixedTypes(array)
     *
     * Tests an array to determine if it contains objects of mixed types.
     *
     * Notes:
     *  1) hasMixedTypes() also checks to make sure the object passed to it is an array and
     *     will throw a TypeError if it isn't.
     *  2) All custom objects in the array must have a constructor definition or the result
     *     may not be valid.
     *
     * In:
     *  array [Array]   The array object to test
     * Out:
     *  [bool]  Boolean indicating whether or not mixed-type objects are present
     */
    hasMixedTypes = function (array) {
        if (Object.prototype.toString.call(array) !== "[object Array]")
            throw new TypeError("Utils.js: hasMixedTypes: Object passed is not an array!");

        var objType = array[0].constructor,     // Get object's constructor (can't use the Object.prototype
                                                // test for custom constructed objects)
            hasMixed = false;

        for (var i = 1; i < array.length; i += 1) {
            // Test object's "type" by comparing constructors
            // If different constructors then different "types", break and return true
            if (objType != array[i].constructor) {
                hasMixed = true;
                break;
            }
        }

        return hasMixed;
    },

    /* getArgumentList(fn)
     *
     * Retrieves the list of parameters (arguments) for a given function. Note
     * that an empty array will be returned if the function declaration includes
     * no arguments. Throws a TypeError exception if the object passed is not a
     * function object.
     *
     * Adapted from Jack Allen's solution at
     *  http://stackoverflow.com/questions/1007981/how-to-get-function-parameter-names-values-dynamically-from-javascript
     *
     * In:
     *  fn [Function]   Function from which to retrieve arguments list
     * Out:
     *  [Array]     Arguments list
     */
    getArgumentList = function (fn) {
        // Throw exception if object passed isn't a function
        if (Object.prototype.toString.call(fn) != "[object Function]")
            throw new TypeError("Utils.js: getArgumentList: Object passed is not a function!");

        var
            // Convert function to string representation and strip out comments
            fnStr = fn
                .toString()
                .replace(/((\/\/.*$)|(\/\*[\s\S]*?\*\/))/mg, ""),

            // Get everything between first occurences of '(' and ')'
            result = fnStr
                .slice(fnStr.indexOf("(") + 1, fnStr.indexOf(")"))
                .match(/([^\s,]+)/g);

        // Return empty array if no arguments are found
        if (result === null)
            result = [];

        return result;
    },

    /* using(obj, fn)
     *
     * Alternative to deprecated 'with' keyword, executes the given function on the passed object. Public properties
     * on the object will be accessible through the 'this' reference from inside the passed function.
     *
     * In:
     *  obj [Object]    Object providing execution context
     *  fn  [Function]  Function containing code to execute
     * Out:
     *  undefined
     *
     * Usage:
     *      using(someObj, function() {
     *          // Code to execute on object
     *          ...
     *      });
     */
    using = function (obj, fn) {
        if (Object.prototype.toString.call(obj) != "[object Object]")
            throw new TypeError("Utils.js: using: Invalid type passed as object; not of type object!");
        if (Object.prototype.toString.call(fn) != "[object Function]")
            throw new TypeError("Utils.js: using: Invalid type passed as function; not of type function!");

        Object.defineProperty(obj, "_using_", {
            configurable: true,
            value: fn
        });
        obj._using_();
        delete obj._using_;
    };