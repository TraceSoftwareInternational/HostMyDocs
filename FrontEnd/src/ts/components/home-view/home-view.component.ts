import * as Clipboard from 'clipboard';

import { ActivatedRoute, Params, Router } from '@angular/router';
import { AfterViewChecked, Component, ElementRef, OnInit, ViewChild } from '@angular/core';

import { Location } from '@angular/common';
import { Observable } from 'rxjs/Observable';
import { ProjectInfo } from '../../models/ProjectInfo';

@Component({
    selector: 'home-view',
    templateUrl: './template.html',
    styleUrls: ['./styles.sass']
})

export class HomeView implements OnInit, AfterViewChecked {
    @ViewChild('copyButton') copyElement: ElementRef;

    /**
     * Information about the current documentation (project name, version and language)
     */
    currentState: ProjectInfo;

    /**
     * Path to the index file to display in the DocumentationViewer
     */
    indexFileToDisplay: string;

    /**
     * Link to the current page/link in the current documentation
     */
    currentLink: string;

    /**
     * Full URL to the archive to download
     */
    downloadLink: string;

    /**
     * Full URL to the current documentation
     */
    sharingLink: string;

    /**
     * Helper to hide or show side navigation
     */
    hideSidenav = false;

    /**
     * Parameters that will be appended to the current URL
     */
    urlParams: string

    /**
     * Clipboard.js instance
     */
    clipboard: Clipboard;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private location: Location
    ) {}

    /**
     * Tries to read URL params to set a certain state.
     */
    ngOnInit() : void {
        this.route.params.subscribe((val) => {
            this.currentState = JSON.parse(JSON.stringify(val), ProjectInfo.reviver);

            if (this.currentState.isValid()) {
                this.hideSidenav = true;
            }

            this.openDocumentation(this.currentState);
        })
    }

    /**
     * Initialize clipboard.js instance
     */
    ngAfterViewChecked() : void {
        if (this.clipboard === undefined && this.copyElement !== undefined) {
            this.clipboard = new Clipboard(`#${this.copyElement.nativeElement.id}`);
        }
    }

    /**
     * Change the state of to boolean that control sidenav visibility
     */
    toggleSidenav() : void {
        this.hideSidenav = ! this.hideSidenav;
    }

    /**
     * Change the current URL using this.urlParams content
     */
    updateUrl() : void {
        this.location.replaceState('/view;' + this.currentState.getMatrixNotation());
    }

    /**
     * Update ths.currentState currentUrl and then update URL
     */
    setCurrentNavigationPage(url: string) : void {
        this.currentState.setCurrentPage(encodeURIComponent(url));
        this.updateUrl();
    }

    /**
     * Receive event and propagate it to the documentation-viewer component
     */
    openDocumentation(event: ProjectInfo) : void {
        this.currentState = event;

        this.indexFileToDisplay = event.getBestURL();

        this.downloadLink = window.location.origin + event.getArchiveFile();
        this.sharingLink  = window.location.origin + event.getMatrixNotation();

        this.updateUrl();
    }
}
