import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';

import { User } from '../models/user';

@Injectable({ providedIn: 'root' })
export class UserService {
    constructor(private http: HttpClient) { }

    get(id: number) {
        return this.http.get<any>(`${environment.apiUrl}/user/${id}`);
    }

    getAll() {
        return this.http.get<any>(`${environment.apiUrl}/user`);
    }

    update(user: any) {
        return this.http.put<any>(`${environment.apiUrl}/user/${user.id}`, {
            name: user.name,
            email: user.email,
        });
    }

    updatePassword(payload: any) {
        console.log(payload)
        return this.http.put<any>(`${environment.apiUrl}/changePassword`, { ...payload });
    }

    register(user: User) {
        return this.http.post<any>(`${environment.apiUrl}/register`, user);
    }

    delete(id: number) {
        return this.http.delete<any>(`${environment.apiUrl}/users/${id}`);
    }
}