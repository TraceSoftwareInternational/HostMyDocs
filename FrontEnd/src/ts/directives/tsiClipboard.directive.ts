import * as Clipboard from 'clipboard'

import { Directive, ElementRef, OnDestroy, OnInit } from '@angular/core';

/**
 * To work this directive must be attached to an element that have a valid id attribute.
 * Text to copy should be in the data-clipboard-text attribute
 */
@Directive({
    selector: '[tsiClipboard]'
})
export class TsiClipboard implements OnInit, OnDestroy {
    /**
     * Local instance of Clipboard.js
     */
    clipboard: Clipboard;

    constructor(private attachedElement: ElementRef) {}

    ngOnInit() {
        let selector = '#' + this.attachedElement.nativeElement.id;
        this.clipboard = new Clipboard(selector);
    }

    ngOnDestroy() {
        if (this.clipboard) {
            this.clipboard.destroy();
        }
    }
}
