import { Heart, MessageCircle, Volume2, VolumeX, X, Send, Maximize2, Minimize2 } from 'lucide-react';

const s = { size: 26, strokeWidth: 2 };

export function IconHeart({ on = false }) {
  return <Heart {...s} fill={on ? 'currentColor' : 'none'} />;
}

export function IconComment({ size } = {}) {
  return <MessageCircle size={size || 26} strokeWidth={2} />;
}

export function IconMuted() {
  return <VolumeX {...s} />;
}

export function IconSound() {
  return <Volume2 {...s} />;
}

export function IconClose({ size = 22 }) {
  return <X size={size} strokeWidth={2} />;
}

export function IconSend() {
  return <Send size={20} strokeWidth={2} />;
}

export function IconFullscreen({ on = false }) {
  const I = on ? Minimize2 : Maximize2;
  return <I size={22} strokeWidth={2} />;
}
