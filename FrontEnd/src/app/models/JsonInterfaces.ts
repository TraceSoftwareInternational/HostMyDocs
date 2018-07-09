export interface JSONLanguage {
    name: string;
    archivePath: string;
    indexPath: string;
}

export interface JSONVersion {
    number: string;
    languages: JSONLanguage[];
}

export interface JSONProject {
    name: string;
    versions: JSONVersion[];
}

export interface JSONProjectInfo {
    project: string;
    version: string;
    language: string;
    currentPage: string;
}
