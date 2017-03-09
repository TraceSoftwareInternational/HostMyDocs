const AddProjectController = require('./controllers/AddProject');
const ListProjectsController = require('./controllers/ListProjects');

const grpc = require('grpc');

let storagePath = null;

/**
 * Main function of the gRPC server.
 * It binds controllers to the rpc functions defined in the proto file.
 *
 * @param port network port where the server will listen for incoming connection
 * @param path where the uploaded files will be extracted and stored
 */
exports.start = (port, path) => {
    const protoDescriptor = grpc.load(__dirname + '/../../../models/models.proto');

    // JavaScript representation of messages and Service defined in the roto file
    const hostMyDocs = protoDescriptor.hostMyDocs;
    const server = new grpc.Server();

    storagePath = path;

    server.addProtoService(hostMyDocs.ProjectService.service, {
        getProjects: (call, callback) => {
            ListProjectsController.process(storagePath, call, callback)
        },
        addProject: (call, callback) => {
            AddProjectController.process(storagePath, call, callback)
        }
    });

    server.bind('0.0.0.0:' + port, grpc.ServerCredentials.createInsecure())
    server.start();
}
