import { Component, Input, ViewChild, OnChanges, SimpleChange, ElementRef } from '@angular/core';

@Component({
    selector: 'documentation-viewer',
    templateUrl: './template.html'
})
export class DocumentationViewer implements OnChanges {
    @Input() indexFile = '';

    @ViewChild('iframe') frame: ElementRef;

    isProjectLoaded: boolean = false;

    ngOnChanges(changes: {[propKey: string]: SimpleChange}) {
        for(let propName in changes) {
            let changedProp = changes[propName];

            if (changedProp.currentValue !== undefined) {
                this.indexFile = changedProp.currentValue;
                this.isProjectLoaded = true;
            }
        }
    }
}
