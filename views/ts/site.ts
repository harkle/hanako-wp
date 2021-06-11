import { Debug } from '../../views/twig/components/Debug';
import { Empty } from '../../views/twig/components/Empty';
import { Menu } from '../../views/twig/components/Menu';
import { ModuleDemo } from '../../views/twig/modules/demo';
import { Page } from '../../views/twig/pages/root/page';
(new Debug()).init();
(new Empty()).init();
(new Menu()).init();
(new ModuleDemo()).init();
(new Page()).init();