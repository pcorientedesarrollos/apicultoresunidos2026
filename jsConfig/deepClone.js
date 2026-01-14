function deepClone(obj) {
    if (obj === null || typeof obj !== 'object') {
        return obj;
    }

    if (Array.isArray(obj)) {
        var cloneArray = [];
        for (var i = 0; i < obj.length; i++) {
            cloneArray[i] = deepClone(obj[i]);
        }
        return cloneArray;
    }

    var cloneObj = {};
    for (var key in obj) {
        if (obj.hasOwnProperty(key)) {
            cloneObj[key] = deepClone(obj[key]);
        }
    }

    return cloneObj;
}
