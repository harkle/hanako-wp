import { AutoReload } from '../../views/ts/components/AutoReload';
import { Carousel } from '../../views/ts/components/Carousel';
import { Collapse } from '../../views/ts/components/Collapse';
import { LazyLoader } from '../../views/ts/components/LazyLoader';
import { ScrollSpy } from '../../views/ts/components/ScrollSpy';
import { Menu } from '../../views/twig/components/Menu';

(new AutoReload()).init();
(new Carousel()).init();
(new Collapse()).init();
(new LazyLoader()).init();
(new ScrollSpy()).init();
(new Menu()).init();
