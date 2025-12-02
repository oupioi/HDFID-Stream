/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via Webpack Encore.
 */
import './styles/app.scss';
import { Application } from '@hotwired/stimulus';
import SearchController from './controllers/search_controller.js';
import ModalController from './controllers/modal_controller.js';

// Start Stimulus
const application = Application.start();
application.register('search', SearchController);
application.register('modal', ModalController);

console.log('Let\'s go!');
