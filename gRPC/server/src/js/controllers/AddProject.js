/**
 * @param call object that contains request information
 * @param callback function to be invoked to terminate client request.
 *     it has 2 parameters : error and response
 */
exports.process = function (call, callback) {

    console.log(call.request);

    callback(null, {
        success: true
    })
}

/**
 *
 * @param {Project} project object that is found in gRPC call object
 */
function checkParams(request) {

}
