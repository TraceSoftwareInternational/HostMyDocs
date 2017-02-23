import { Component, OnInit, AfterViewChecked, ElementRef, ViewChild } from '@angular/core';
import { Location } from '@angular/common';
import { Router, ActivatedRoute, Params } from '@angular/router';

import { Observable } from 'rxjs/Observable';

import { ProjectInfo } from '../../models/ProjectInfo';

import * as Clipboard from 'clipboard';

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
     * Full URL to the archive to download
     */
    downloadLink: string;

    /**
     * Helper to hide or show side navigation
     */
    hideSidenav = false;

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
    ngOnInit() {
        this.route.params.subscribe((val) => {
            this.currentState = JSON.parse(JSON.stringify(val), ProjectInfo.reviver);

            if (this.currentState.isValid()) {
                this.hideSidenav = true;
            }
        })
    }

    /**
     * Initialize clipboard.js instance
     */
    ngAfterViewChecked() {
        if (this.clipboard === undefined && this.copyElement !== undefined) {
            this.clipboard = new Clipboard(`#${this.copyElement.nativeElement.id}`);
        }
    }

    /**
     * Change the state of to boolean that control sidenav visibility
     */
    toggleSidenav() {
        this.hideSidenav = ! this.hideSidenav;
    }

    /**
     * Receive event and propagate it to the documentation-viewer component
     */
    openDocumentation(event: ProjectInfo) : void {
        this.currentState = event;

        this.location.replaceState(`/view;project=${event.getProject()};version=${event.getVersion()};language=${event.getLanguage()}`);

        this.indexFileToDisplay = event.getIndexFile();
        this.downloadLink = window.location.origin + event.getArchiveFile();
    }
}
