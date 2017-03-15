import { Pipe, PipeTransform } from '@angular/core';

import { Project } from '../models/Project';

@Pipe({
    name: 'filterProjects'
})
export class FilterProjects implements PipeTransform {
    transform(items: Project[], filter: string) : any {
         return items.filter(project => project.name.toLowerCase().indexOf(filter.toLocaleLowerCase()) !== -1);
    }
}
