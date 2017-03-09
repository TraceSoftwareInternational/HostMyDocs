/**
 * Return a client associated to the ProjectService defined in the proto file
 */
exports.client = (server, port) => {
    const grpc = require('grpc');
    const protoDescriptor = grpc.load(__dirname + '/../../../models/models.proto');

    // JavaScript representation of messages and Service defined in the roto file
    const hostMyDocs = protoDescriptor.hostMyDocs;
    const client = new hostMyDocs.ProjectService(server + ':' + port, grpc.credentials.createInsecure());

    return client;
}
