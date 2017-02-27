import {
    Component,
    ElementRef,
    EventEmitter,
    Input,
    OnChanges,
    Output,
    SimpleChange,
    ViewChild,
} from '@angular/core';

@Component({
    selector: 'documentation-viewer',
    templateUrl: './template.html'
})
export class DocumentationViewer implements OnChanges {
    /**
     * Full path to the documentation to preview
     */
    @Input() indexFile = '';

    /**
     * Representation of to the iframe DOM node
     */
    @ViewChild('iframe') frame: ElementRef;

    /**
     * helper to know if viewer is empty or not
     */
    isProjectLoaded: boolean = false;

    /**
     * Emit relative page url each time user navigate in iframe
     */
    @Output() currentRelativeURL = new EventEmitter<string>();

    /**
     * watching changes to reflect on this.indexFile
     */
    ngOnChanges(changes: {
        [propKey: string]: SimpleChange
    }) {
        for (let propName in changes) {
            let changedProp = changes[propName];

            if (changedProp.currentValue !== undefined) {
                this.indexFile = changedProp.currentValue;
                this.isProjectLoaded = true;
            }
        }
    }

    srcWatcher(iframe) {
        let fullUrl = iframe.contentWindow.location.href;
        let relativeUrl = fullUrl.replace(iframe.contentWindow.location.origin, '');        

        if(relativeUrl !== undefined) {
            this.currentRelativeURL.emit(relativeUrl);
        }
    }
}
