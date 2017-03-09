const AddProjectController = require('./controllers/AddProject');
const ListProjectsController = require('./controllers/ListProjects');

const grpc = require('grpc');
const protoDescriptor = grpc.load(__dirname + '/../../../models/models.proto');

// JavaScript representation of messages and Service defined in the roto file
const hostMyDocs = protoDescriptor.hostMyDocs;

const server = new grpc.Server();


/**
 * Main function of the gRPC server.
 * It binds controllers to the rpc functions defined in the proto file.
 */
function main() {
    server.addProtoService(hostMyDocs.ProjectService.service, {
        getProjects: ListProjectsController.process,
        addProject: AddProjectController.process
    });

    server.bind('0.0.0.0:50051', grpc.ServerCredentials.createInsecure())
    server.start();
}

main();
