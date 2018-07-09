import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UrlExistenceService {
    constructor(private http: HttpClient) { }

    /**
     * Return true if the URL exist and return a 200 code
     */
    public async check(url: string): Promise<boolean> {
        const response = this.http.head(url);

        return new Promise<boolean>((resolve) => {
            response.subscribe(
                (observer) => {
                    resolve(true);
                },
                (error) => {
                    resolve(false);
                }
            );
        });
    }
}
