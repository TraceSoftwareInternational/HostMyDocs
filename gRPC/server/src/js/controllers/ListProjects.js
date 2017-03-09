exports.process = (call, callback) => {
    this.call = call;

    checkParams();

    callback(null, {
        success: true
    })
}
