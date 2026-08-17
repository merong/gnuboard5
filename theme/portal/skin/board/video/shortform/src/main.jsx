import { createRoot } from 'react-dom/client';
import { createElement } from 'react';
import App from './App.jsx';
import './styles.css';

const roots = new WeakMap();

export function mount(el, options = {}) {
  if (!el) return null;
  const items = Array.isArray(options.items) ? options.items : [];
  const startIndex = Number(options.startIndex) || 0;
  let root = roots.get(el);
  if (!root) {
    root = createRoot(el);
    roots.set(el, root);
  }
  root.render(createElement(App, { items, startIndex }));
  return {
    unmount() {
      root.unmount();
      roots.delete(el);
    },
  };
}
