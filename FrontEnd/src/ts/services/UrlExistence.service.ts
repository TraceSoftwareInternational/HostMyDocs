import { Injectable }     from '@angular/core';
import { Http, Response } from '@angular/http';

import { Observable } from 'rxjs/Observable';

@Injectable()
export class UrlExistence {
    constructor(private http: Http) {}

    /**
     * Return true if the URL exist and return a 200 code
     */
    public check(url: string) : Observable<boolean> {
        return this.http.head(url)
            .map(() => { return true })
            .catch(() => Observable.throw(false))
    }
}
