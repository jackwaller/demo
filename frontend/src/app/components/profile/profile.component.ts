import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { first } from 'rxjs/operators';
import { User } from 'src/app/models/user';
import { AlertService } from '../../services/alert.service';
import { UserService } from '../../services/user.service';
import { AuthenticationService } from '../../services/auth.service';

@Component({ templateUrl: './profile.component.html' })

export class ProfileComponent implements OnInit {
  currentUser: User;
  user: any;
  userId: number;
  loading = false;
  loadingMessage: string;
  updateUser = false;
  updatePassword = false;

  detailForm: FormGroup;
  passwordForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private authenticationService: AuthenticationService,
    private userService: UserService,
    private alertService: AlertService,
    private route: ActivatedRoute
  ) {
    this.currentUser = this.authenticationService.currentUserValue;
  }

  ngOnInit(): void {
    this.route.params
      .subscribe(
        (params: any) => {
          this.userId = params['id'];
          this.loadUser();
        }
      );
  }

  showUpdateUser(value) {
    this.updateUser = value;

    this.detailForm = this.formBuilder.group({
      name: [this.user.name, Validators.required],
      email: [this.user.email, Validators.required],
    });
  }

  showUpdatePassword(value) {
    this.updatePassword = value;

    this.passwordForm = this.formBuilder.group({
      password: ['', Validators.required],
      newPassword: ['', [Validators.required, Validators.minLength(6)]],
      c_password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  private loadUser() {
    this.loading = true;
    this.loadingMessage = 'Retrieving profile';

    this.userService.get(this.userId)
      .pipe(first())
      .subscribe(
        user => {
          this.loading = false;

          if (user.success) {
              this.user = user.result;
          }
        },
        error => {
            this.alertService.error(error);
            this.loading = false;
        });
  }

  onSubmitDetails() {
    // reset alerts on submit
    this.alertService.clear();

    // stop here if form is invalid
    if (this.detailForm.invalid) {
        return;
    }

    this.loading = true;
    this.loadingMessage = 'Updating profile';

    this.userService.update({id: this.user.id, ...this.detailForm.value })
      .pipe(first())
      .subscribe(
          response => {
              this.loading = false;
              this.updateUser = false;

              if (response.success) {
                this.alertService.success('User has successfully updated', true);
                return this.user = response.result;
              }

              return this.alertService.error(response.error.message);

          },
          error => {
              this.alertService.error(error);
              this.loading = false;
          });
  }

  onSubmitPassword() {
    // reset alerts on submit
    this.alertService.clear();

    // stop here if form is invalid
    if (this.passwordForm.invalid) {
        return;
    }

    this.loading = true;

    this.userService.updatePassword(this.passwordForm.value)
      .pipe(first())
      .subscribe(
          data => {
            this.alertService.success('Password has been successfully updated', true);
            this.loading = false;
          },
          error => {
              this.alertService.error(error);
              this.loading = false;
          });
  }

}
