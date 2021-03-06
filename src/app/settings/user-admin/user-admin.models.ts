import { User }  from '../../shared/index';

export class UserDisplay extends User {
    public default_board_name: string;
    public security_level_name: string;
    public can_admin: boolean;
}

export class ModalUser extends UserDisplay {
    public password: string = '';
    public verifyPassword: string = '';
    public boardAccess: Array<string> = [];

    constructor(user: User) {
        super(user.default_board_id, user.email, user.id,
              user.last_login, user.security_level, user.user_option_id,
              user.username, user.board_access);

        user.board_access.forEach((id) => {
            this.boardAccess.push('' + id);
        });
    }
}

export class ModalProperties {
    constructor(public title: string,
        public prefix: string,
        public user: ModalUser) {
    }
}

