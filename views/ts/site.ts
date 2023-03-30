import { AutoReload } from '../../views/ts/components/AutoReload';
import { Carousel } from '../../views/ts/components/Carousel';
import { Collapse } from '../../views/ts/components/Collapse';
import { Dropdown } from '../../views/ts/components/Dropdown';
import { LazyLoader } from '../../views/ts/components/LazyLoader';
import { ScrollSpy } from '../../views/ts/components/ScrollSpy';
import { DarkMode } from '../../views/twig/components/DarkMode';
import { Menu } from '../../views/twig/components/Menu';

(new AutoReload()).init();
(new Carousel()).init();
(new Collapse()).init();
(new Dropdown()).init();
(new LazyLoader()).init();
(new ScrollSpy()).init();
(new DarkMode()).init();
(new Menu()).init();
