import { Component, Input, Output, EventEmitter } from '@angular/core'
import { OnInit } from '@angular/core';

import { ProjectsService } from '../../services/projects.service';

import { Project } from '../../models/Project';
import { ProjectChangeEvent } from '../../models/ProjectChangeEvent';

@Component({
    selector: 'project-tree',
    templateUrl: './template.html',
    styleUrls: ['./styles.sass'],
    providers: [ProjectsService]
})
export class ProjectsTree implements OnInit {
    /**
     * Project to open provided by URL
     */
    @Input() projectFromRoute: string;

    /**
     * Version to open provided by URL
     */
    @Input() versionFromRoute: string;

    /**
     * Language to open provided by URL
     */
    @Input() languageFromRoute: string;

    /**
     * Event emitter to notify parent component of archivePath and indexPath from the selected language
     */
    @Output() onProjectSelection = new EventEmitter<ProjectChangeEvent>();

    private projects: Array<Project> = [];

    constructor(private projectsService: ProjectsService) {}

    /**
     * Fetch projects from BackEnd at component initialization
     */
    ngOnInit(): void {
        this.projectsService.getProjects().subscribe(
            projects => this.projects = projects,
            error => console.log('error', error)
        );
    }

    /**
     * Given a project, return its highest version
     */
    getLastVersion(project: Project) : string {
        let lastVersionIndex: number = project.versions.length - 1;

        return project.versions[lastVersionIndex].number;
    }

    /**
     * Sending an event to the parent, to display documentation
     */
    notifyParent(indexPath: string, archivePath: string) : void {
        this.onProjectSelection.emit(new ProjectChangeEvent(indexPath, archivePath));
    }
}
