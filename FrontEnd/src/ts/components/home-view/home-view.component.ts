import { Component } from '@angular/core';

import { ProjectChangeEvent } from '../../models/ProjectChangeEvent';

@Component({
    selector: 'home-view',
    templateUrl: './template.html',
    styleUrls: ['./styles.sass']
})

export class HomeView {
    /**
     * Path to the index file to display in the DocumentationViewer
     */
    indexFileToDisplay: string;

    /**
     * Receive event and propagate it to the documentation-viewer component
     */
    openDocumentation(event: ProjectChangeEvent) : void {
        this.indexFileToDisplay = event.getIndexPath();
    }
}
