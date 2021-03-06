/* global expect AuthServiceMock SettingsServiceMock ModalServiceMock NotificationsServiceMock */
var path = '../../../../build/settings/board-admin/',
    BoardAdmin = require(path + 'board-admin.component.js').BoardAdmin,
    DragulaService = require('../../../../node_modules/ng2-dragula/components/dragula.provider.js')
        .DragulaService;

describe('BoardAdmin', () => {
    var boardAdmin;

    beforeEach(() => {
        boardAdmin = new BoardAdmin(AuthServiceMock, new ModalServiceMock(),
            new SettingsServiceMock(), new NotificationsServiceMock(),
            new DragulaService());
    });

    it('has a function to get a color', () => {
        var color = boardAdmin.getColor({ defaultColor: 'test' });
        expect(color).to.equal('test');
    });

    it('implements ngAfterContentInit', () => {
        expect(boardAdmin.ngAfterContentInit).to.be.a('function');
    });
});

