import { Debug } from '../components/Debug';
import { $ } from 'hanako-ts/dist-legacy/Framework';

(new Debug()).init();

window.setInterval(() => {
  let elements = $('body *');

  Debug.log(elements.get(Math.floor(Math.random() * elements.length)));
}, 2000);
