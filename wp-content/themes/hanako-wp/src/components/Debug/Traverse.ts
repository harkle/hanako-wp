import { $ } from 'hanako-ts/dist/Framework';
import { Collection } from 'hanako-ts/dist/Collection';

function formatNode(node: HTMLElement) {
  let id = (node.id) ? '#' + node.id : '';
  let classes = (node.className) ? '.' + node.className : '';

  return '<span class="key">' + node.tagName.toLowerCase() + '</span><span class="attr">' + id + classes + '</span>';
}

export function traverse(object: any, level?: number): string {
  if (!level) level = 0;
  if (level > 2) return;

  let open = '';
  let close = '';
  let message = '';

  if (object instanceof Collection || (typeof object == 'object' && !(object instanceof Collection))) {
    open = '{ ';
    close = ' }';
  }
  if (object instanceof Event) {
    return '[Event "' + object.type + '"]'
  }

  if (object instanceof Node) {
    return formatNode(<HTMLElement>object);
  }

  let properties: string[] = [];
  for (const property in object) {
    if (object[property] instanceof Array) {
      properties.push('<span class="key">' + property + '</span>: [ ' + traverse(object[property], ++level) + ' ]');
    } else if (object[property] instanceof Node) {
      properties.push(formatNode(object[property]));
    } else {
      properties.push('<span class="key">' + property + '</span>: ' + object[property]);
    }
  }

  return open + properties.join(', ') + close;
}
