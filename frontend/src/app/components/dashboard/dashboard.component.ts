import { Component, OnInit } from '@angular/core';
import { first } from 'rxjs/operators';
import { User } from '../../models/user';
import { AlertService } from '../../services/alert.service';
import { UserService } from '../../services/user.service';
import { AuthenticationService } from '../../services/auth.service';
import { Router } from '@angular/router';

@Component({ templateUrl: 'dashboard.component.html' })

export class DashboardComponent implements OnInit {
    currentUser: User;
    users = [];
    loading = false;

    constructor(
        private authenticationService: AuthenticationService,
        private userService: UserService,
        private alertService: AlertService,
        private router: Router,
    ) {
        this.currentUser = this.authenticationService.currentUserValue;
    }

    ngOnInit() {
        this.loadAllUsers();
    }

    deleteUser(id: number) {
        this.userService.delete(id)
            .pipe(first())
            .subscribe(
                data => {
                    this.loadAllUsers()
                },
                error => {
                    this.alertService.error(error);
                    this.loading = false;
                });
    }

    viewUser(id: number) {
        // redirect to profile page
        this.router.navigate([`/profile/${id}`]);
    }

    private loadAllUsers() {
        this.loading = true;

        this.userService.getAll()
            .pipe(first())
            .subscribe(users => {
                this.loading = false;

                if (users.success) {
                    this.users = users.result;
                }
            });
    }
}