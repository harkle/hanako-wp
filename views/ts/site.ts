import { $ } from 'hanako-ts/dist-legacy/Framework';
import { AutoReload } from '../../views/ts/components/AutoReload';
import { LazyLoader } from '../../views/ts/components/LazyLoader';
import { Menu } from '../../views/twig/components/Menu';
import { RGPDConsent } from '../../views/twig/components/RGPDConsent'; 

(new AutoReload()).init();
(new LazyLoader()).init();
(new Menu()).init();
(new RGPDConsent()).init(); 
