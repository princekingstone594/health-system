import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                brand: {
                    50:  '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                    950: '#042f2e',
                },
                surface: {
                    DEFAULT: '#ffffff',
                    muted:   '#f8fafc',
                    border:  '#e2e8f0',
                    dark:    '#0f172a',
                    'dark-2':'#1e293b',
                    'dark-3':'#334155',
                },
                'glass': {
                    DEFAULT: 'rgba(255, 255, 255, 0.7)',
                    'border': 'rgba(255, 255, 255, 0.2)',
                },
            },
            boxShadow: {
                card:     '0 1px 3px 0 rgb(0 0 0 / 0.04), 0 1px 2px -1px rgb(0 0 0 / 0.04)',
                'card-hover': '0 10px 25px -5px rgb(0 0 0 / 0.08), 0 8px 10px -6px rgb(0 0 0 / 0.04)',
                'card-lg': '0 20px 40px -12px rgb(0 0 0 / 0.10)',
                sidebar:   '4px 0 24px -4px rgb(0 0 0 / 0.08)',
                'glow-brand':  '0 0 20px -2px rgb(20 184 166 / 0.3)',
                'glow-violet': '0 0 20px -2px rgb(139 92 246 / 0.3)',
                'glow-sky':    '0 0 20px -2px rgb(56 189 248 / 0.3)',
                'glow-amber':  '0 0 20px -2px rgb(245 158 11 / 0.3)',
                'glow-emerald':'0 0 20px -2px rgb(16 185 129 / 0.3)',
                'soft':    '0 2px 15px -3px rgb(0 0 0 / 0.07), 0 10px 20px -2px rgb(0 0 0 / 0.04)',
                'inner-lg':'inset 0 2px 4px 0 rgb(0 0 0 / 0.04)',
            },
            borderRadius: {
                xl:  '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
                '4xl': '2rem',
            },
            animation: {
                'fade-in':      'fadeIn 0.3s ease-out',
                'fade-in-up':   'fadeInUp 0.4s ease-out',
                'fade-in-down': 'fadeInDown 0.3s ease-out',
                'slide-up':     'slideUp 0.4s ease-out',
                'slide-down':   'slideDown 0.3s ease-out',
                'scale-in':     'scaleIn 0.2s ease-out',
                'scale-out':    'scaleOut 0.15s ease-in',
                'shimmer':      'shimmer 2s linear infinite',
                'spin-slow':    'spin 2s linear infinite',
                'ping-slow':    'ping 2s cubic-bezier(0, 0, 0.2, 1) infinite',
                'pulse-soft':   'pulseSoft 2s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%':   { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%':   { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideUp: {
                    '0%':   { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideDown: {
                    '0%':   { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%':   { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                scaleInOut: {
                    '0%':   { opacity: '1', transform: 'scale(1)' },
                    '100%': { opacity: '0', transform: 'scale(0.95)' },
                },
                shimmer: {
                    '0%':   { backgroundPosition: '-1000px 0' },
                    '100%': { backgroundPosition: '1000px 0' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%':      { opacity: '0.7' },
                },
            },
            backgroundImage: {
                'gradient-brand': 'linear-gradient(135deg, #14b8a6 0%, #0d9488 100%)',
                'gradient-dark':  'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)',
                'gradient-violet': 'linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%)',
                'gradient-sky':    'linear-gradient(135deg, #0284c7 0%, #38bdf8 100%)',
                'gradient-amber':  'linear-gradient(135deg, #d97706 0%, #fbbf24 100%)',
                'gradient-emerald':'linear-gradient(135deg, #059669 0%, #34d399 100%)',
                'gradient-rose':   'linear-gradient(135deg, #e11d48 0%, #fb7185 100%)',
                'radial-brand':    'radial-gradient(ellipse at top left, rgba(20,184,166,0.15) 0%, transparent 50%)',
                'radial-violet':   'radial-gradient(ellipse at top right, rgba(139,92,246,0.15) 0%, transparent 50%)',
                'grid-pattern':    'linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px)',
                'grid-white':      'linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px)',
            },
            transitionDuration: {
                '250': '250ms',
                '350': '350ms',
                '400': '400ms',
            },
            transitionTimingFunction: {
                'bounce-in':  'cubic-bezier(0.34, 1.56, 0.64, 1)',
                'bounce-out': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
            },
        },
    },

    plugins: [forms],
};