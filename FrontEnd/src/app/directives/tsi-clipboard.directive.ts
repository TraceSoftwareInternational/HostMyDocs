import * as Clipboard from 'clipboard';
import { EventEmitter, Directive, ElementRef, OnDestroy, OnInit, Output } from '@angular/core';

@Directive({
  selector: '[tsiClipboard]'
})
export class ClipboardDirective implements OnInit, OnDestroy {

    /**
       * Local instance of Clipboard.js
       */
    clipboard: Clipboard;

    /**
     * Notify parent that the copy was successful by sending the true value
     */
    @Output()
    success: EventEmitter<boolean> = new EventEmitter<boolean>();

    /**
     * Notify parent that the copy was NOT successful by sending the false value
     */
    @Output()
    error: EventEmitter<boolean> = new EventEmitter<boolean>();

    constructor(private attachedElement: ElementRef) { }

    ngOnInit() {
        const selector = '#' + this.attachedElement.nativeElement.id;
        this.clipboard = new Clipboard(selector);

        this.clipboard.on('success', () => this.success.emit(true));
        this.clipboard.on('error', () => this.error.emit(false));
    }

    ngOnDestroy() {
        if (this.clipboard) {
            this.clipboard.destroy();
        }
    }
}
