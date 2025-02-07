
import { startStimulusApp } from '@symfony/stimulus-bridge';
import MyAccountController from './controllers/my_account_controller';
import DeleteOrganisationController from './controllers/delete_organisation_controller';
import MemberDeleteController from './controllers/member_delete_controller';
import MemberSearchController from './controllers/members_search_controller';
import CopyClipboardController from './controllers/copy_clipboard_controller';

const app = startStimulusApp();
app.register('my-account', MyAccountController);
app.register('delete-organisation', DeleteOrganisationController);
app.register('member-delete', MemberDeleteController);
app.register('members-search', MemberSearchController);
app.register('copy-clipboard', CopyClipboardController);
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
