
import { startStimulusApp } from '@symfony/stimulus-bridge';
import MyAccountController from './controllers/my_account_controller';

const app = startStimulusApp();
app.register('my-account', MyAccountController);
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
