import { createRoot } from 'react-dom/client';
import { createElement } from 'react';
import App from './App.jsx';
import './styles.css';

const roots = new WeakMap();

export function mount(el, options = {}) {
  if (!el) return null;
  const props = {
    items: Array.isArray(options.items) ? options.items : [],
    startIndex: Number(options.startIndex) || 0,
    ajaxUrl: options.ajaxUrl || '',
    goodUrl: options.goodUrl || '',
    commentUrl: options.commentUrl || '',
    loginUrl: options.loginUrl || '',
    isMember: !!options.isMember,
    page: Number(options.page) || 1,
    totalPage: Number(options.totalPage) || 1,
  };
  let root = roots.get(el);
  if (!root) {
    root = createRoot(el);
    roots.set(el, root);
  }
  root.render(createElement(App, props));
  return {
    unmount() {
      root.unmount();
      roots.delete(el);
    },
  };
}
