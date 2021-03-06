/* global expect AuthServiceMock NotificationsServiceMock SettingsServiceMock UserSettingsServiceMock */
var path = '../../../../build/settings/user-settings/',
    UserSettings = require(path + 'user-settings.component.js').UserSettings;

describe('UserSettings', () => {
    var userSettings,
        notifications;

    beforeEach(() => {
        notifications = new NotificationsServiceMock();
        userSettings = new UserSettings(AuthServiceMock,
            notifications, new SettingsServiceMock(),
            UserSettingsServiceMock);
    });

    it('resets forms on init', () => {
        userSettings.ngOnInit();

        expect(userSettings.changePassword.current).to.equal('');
        expect(userSettings.changeUsername.newName).to.equal('');
        expect(userSettings.changeEmail.newEmail).to.equal('');
    });

    it('updates user options on change', () => {
        userSettings.userOptions = {};

        ['new_tasks', 'mult_tasks', 'show_anim', 'show_assign']
            .forEach(opt => {
                userSettings.onOptionChange(opt, true);
            });

        expect(userSettings.userOptions.new_tasks_at_bottom).to.equal(false);
        expect(userSettings.userOptions.multiple_tasks_per_row).to.equal(true);
        expect(userSettings.userOptions.show_animations).to.equal(true);
        expect(userSettings.userOptions.show_assignee).to.equal(true);
    });

    it('has a function to update default board', (done) => {
        notifications.noteAdded.subscribe(note => {
            expect(note.type).to.equal('success');
            done();
        });

        userSettings.updateDefaultBoard('1');
    });

    it('has a function to update password', (done) => {
        userSettings.changePassword = {
            current: ''
        };

        var first = true,
            second = true;
        notifications.noteAdded.subscribe(note => {
            if (first) {
                expect(note.type).to.equal('error');
                first = false;
            } else if (second) {
                expect(note.type).to.equal('error');
                second = false;
            } else {
                expect(note.type).to.equal('success');
                done();
            }
        });
        userSettings.updatePassword();

        userSettings.changePassword = {
            current: 'old',
            newPass: 'new',
            verPass: 'err'
        };
        userSettings.updatePassword();

        userSettings.changePassword = {
            current: 'old',
            newPass: 'new',
            verPass: 'new'
        };
        userSettings.updatePassword();
    });

    it('has a function to update username', (done) => {
        userSettings.changeUsername = { newName: '' };

        var first = true;
        notifications.noteAdded.subscribe(note => {
            if (first) {
                expect(note.type).to.equal('error');
                first = false;
            } else {
                expect(note.type).to.equal('success');
                done();
            }
        });
        userSettings.updateUsername();

        userSettings.changeUsername = { newName: 'test' };
        userSettings.updateUsername();
    });

    it('has a function to update email', (done) => {
        userSettings.changeEmail = { newEmail: 'asdf' };

        var first = true;
        notifications.noteAdded.subscribe(note => {
            if (first) {
                expect(note.type).to.equal('error');
                first = false;
            } else {
                expect(note.type).to.equal('success');
                done();
            }
        });
        userSettings.updateEmail();

        userSettings.changeEmail.newEmail = 'test@example.com';
        userSettings.updateEmail();
    });
});

