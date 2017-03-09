/**
 * Set the path where we will list folders
 */
exports.setPath = (path) => {
    this.path = path;
}

exports.process = (call, callback) => {
    this.call = call;

    checkParams();

    callback(null, {
        success: true
    })
}
