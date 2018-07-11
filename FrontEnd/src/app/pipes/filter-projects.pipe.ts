import { Pipe, PipeTransform } from '@angular/core';

import { Project } from '../models/Project';

/**
 * Filter an array
 */
@Pipe({
    name: 'filterProjects',
    pure: true
})
export class FilterProjectsPipe implements PipeTransform {
    transform(items: Project[], filter: string): any {
        return items.filter(project => project.name.toLowerCase().indexOf(filter.toLocaleLowerCase()) !== -1);
    }
}
