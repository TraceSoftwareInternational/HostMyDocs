import { DummyData } from './dummyData.service';

describe('DummyData service', () => {
    let service: DummyData;

    beforeEach(() => {
        service = new DummyData();
    })

    it('#getProjects should return an array', () => {
        let projects = service.getProjects();

        expect(projects).toBeDefined()
    });
});
