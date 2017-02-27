import { Pipe, PipeTransform } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';

/**
 * Simple pipe to make Angular trust a given URL resource
 */
@Pipe({
    name: 'trustUrl'
})
export class TrustUrl implements PipeTransform {
    constructor(private sanitizer: DomSanitizer) {}

    transform(url) {
        return this.sanitizer.bypassSecurityTrustResourceUrl(url);
    }
}
