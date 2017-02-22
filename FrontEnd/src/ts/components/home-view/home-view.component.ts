import { Component, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { Router, ActivatedRoute, Params } from '@angular/router';

import { Observable } from 'rxjs/Observable';

import { ProjectInfo } from '../../models/ProjectInfo';

@Component({
    selector: 'home-view',
    templateUrl: './template.html',
    styleUrls: ['./styles.sass']
})

export class HomeView implements OnInit {
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

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private location: Location
    ) {}

    /**
     * Tries to read URL params to set a certain state
     */
    ngOnInit() {
        this.route.params.subscribe((val) => {
            this.currentState = JSON.parse(JSON.stringify(val), ProjectInfo.reviver);
        })
    }

    /**
     * Return the full URL that the user will be able to share
     */
    getSharingLink(): string {
        return window.location.href;
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
