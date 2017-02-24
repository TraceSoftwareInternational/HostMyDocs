import { Component, EventEmitter, Input, Output } from '@angular/core'

import { Language } from '../../models/Language';
import { OnInit } from '@angular/core';
import { Project }  from '../../models/Project';
import { ProjectInfo } from '../../models/ProjectInfo';
import { ProjectsService } from '../../services/projects.service';
import { Version }  from '../../models/Version';

@Component({
    selector: 'project-tree',
    templateUrl: './template.html',
    styleUrls: ['./styles.sass'],
    providers: [ProjectsService]
})
export class ProjectsTree implements OnInit {
    /**
     * Information about the doc to open
     */
    @Input() existingState: ProjectInfo;

    /**
     * Event emitter to notify parent component of archivePath and indexPath from the selected language
     */
    @Output() onProjectSelection = new EventEmitter<ProjectInfo>();

    private projects: Array<Project> = [];

    constructor(private projectsService: ProjectsService) {}

    /**
     * Fetch projects from BackEnd at component initialization.
     * Also tries to restore a certain state provided by project, version and language
     */
    ngOnInit(): void {
        this.projectsService.getProjects().subscribe(
            projects => this.projects = projects,
            error => console.log(error)
        );
    }

    /**
     * Given a project, return its highest version
     */
    getLastVersion(project: Project) : string {
        let lastVersionIndex = project.versions.length - 1;

        return project.versions[lastVersionIndex].number;
    }

    /**
     * Sending an event to the parent, to display documentation
     */
    notifyParent(project: Project, version: Version, language: Language) : void {
        let state = new ProjectInfo(project.name, version.number, language.name);
        state.setArchiveFile(language.archivePath);
        state.setindexFile(language.indexPath);

        this.onProjectSelection.emit(state);
    }
}
