import { Injectable } from '@angular/core';

@Injectable()
export class DummyData {
    getProjects(): Object {
        return [
            {
                'name': 'libtruc',
                'languages': ['COBOL', 'FORTRAN']
            },
            {
                'name': 'libmachin',
                'languages': ['C', 'C++']
            },
            {
                'name': 'libidule',
                'languages': ['TypeScript', 'CoffeeScript']
            }
        ]
    }
}
