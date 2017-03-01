import * as Clipboard from 'clipboard'

import { EventEmitter, Directive, ElementRef, OnDestroy, OnInit, Output } from '@angular/core';

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

    /**
     * Notify parent that the copy was successful by sending the true value
     */
    @Output('onSuccess') onSuccess: EventEmitter<boolean> = new EventEmitter<boolean>();

    /**
     * Notify parent that the copy was NOT successful by sending the false value
     */
    @Output('onError') onError: EventEmitter<boolean> = new EventEmitter<boolean>();

    constructor(private attachedElement: ElementRef) {}

    ngOnInit() {
        let selector = '#' + this.attachedElement.nativeElement.id;
        this.clipboard = new Clipboard(selector);

        this.clipboard.on('success', () => this.onSuccess.emit(true));
        this.clipboard.on('error', () => this.onError.emit(false));
    }

    ngOnDestroy() {
        if (this.clipboard) {
            this.clipboard.destroy();
        }
    }
}
