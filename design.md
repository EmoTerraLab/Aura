# Manual de Identidad Corporativa y Sistema de Diseño — Aura

## 1. Filosofía de la Marca
**Aura** es una plataforma diseñada para fomentar entornos educativos seguros y armónicos. Su identidad visual se fundamenta en la **tranquilidad, la protección y la claridad**.

*   **Valores Centrales**: Empatía, Transparencia, Seguridad y Humanismo.
*   **Percepción del Usuario**: La interfaz debe sentirse como un espacio seguro y profesional, pero cercano. No es una herramienta administrativa fría, sino un sistema de apoyo emocional y preventivo.
*   **Lenguaje de Diseño**: Inspirado en los principios de *Material You* y la *Human Interface Guidelines (HIG)* de Apple, priorizando superficies suaves, tipografía legible y una paleta de colores orgánica.

---

## 2. Construcción de Logotipos
El logotipo de Aura representa el halo de protección y bienestar que la plataforma busca proyectar sobre la comunidad educativa.

### Variaciones
*   **Logotipo Principal**: Isotipo + Logotipo en color *Primary* (#004F56). Se usa sobre fondos *Surface* o blancos.
*   **Versión Negativa**: Logotipo íntegramente en blanco para fondos oscuros o imágenes complejas.
*   **Isotipo (Símbolo)**: Versión simplificada para favicons, avatares de usuario y espacios reducidos (menos de 80px).

### Directrices de Uso
*   **Área de Respeto**: El espacio libre alrededor del logo debe ser, como mínimo, equivalente a la altura de la letra 'A' de la marca.
*   **Prohibiciones (Don'ts)**:
    *   No estirar ni deformar las proporciones.
    *   No usar sombras paralelas ni efectos de relieve.
    *   No rotar el logotipo.
    *   No usar combinaciones de colores fuera de la paleta oficial.

---

## 3. Sistema Cromático
La paleta de Aura utiliza tonos inspirados en la naturaleza para evocar calma y estabilidad.

| Categoría | Nombre | HEX | RGB | Caso de Uso |
| :--- | :--- | :--- | :--- | :--- |
| **Primary** | Deep Sea | `#004F56` | `(0, 79, 86)` | Identidad principal, botones de acción primaria, headers. |
| **Secondary** | Slate Blue | `#3A6478` | `(58, 100, 120)` | Acciones secundarias, iconos de soporte. |
| **Tertiary** | Amber Earth | `#6D3A10` | `(109, 58, 16)` | Acentos, llamadas a la atención suaves. |
| **Background** | Ice Flow | `#F7FAFA` | `(247, 250, 250)` | Fondo general de la aplicación. |
| **Surface** | Pure White | `#FFFFFF` | `(255, 255, 255)` | Tarjetas, contenedores, campos de entrada. |
| **Error** | Crimson | `#BA1A1A` | `(186, 26, 26)` | Alertas críticas, validaciones fallidas. |
| **Success** | Forest | `#2D6A4F` | `(45, 106, 79)` | Confirmaciones, estados positivos. |

---

## 4. Jerarquía Tipográfica
La tipografía elegida es **Manrope**, una fuente sans-serif geométrica que equilibra la precisión técnica con formas humanistas.

### Escala de Tipos
```css
/* Implementación en variables CSS */
:root {
  --font-main: 'Manrope', sans-serif;
  
  --text-h1: 2rem;      /* 32px - 700 Bold */
  --text-h2: 1.5rem;    /* 24px - 600 SemiBold */
  --text-body-lg: 1.125rem; /* 18px - 400 Regular */
  --text-body-md: 1rem;     /* 16px - 400 Regular */
  --text-caption: 0.75rem;  /* 12px - 600 SemiBold */
}
```

*   **Display / H1**: 32px | Interlineado 1.2 | Espaciado -0.02em. Para títulos principales.
*   **Subheadings / H2**: 24px | Interlineado 1.3 | Espaciado -0.01em. Para secciones.
*   **Body**: 16px | Interlineado 1.6 | Espaciado 0.01em. Para lectura general.
*   **Label Caps**: 12px | Interlineado 1.0 | Espaciado 0.08em (Uppercase). Para etiquetas y botones.

---

## 5. Componentes de Interfaz e Iconografía
Los componentes de Aura se caracterizan por sus bordes generosamente redondeados y sombras sutiles.

### Elementos Interactivos
*   **Botones**:
    *   *Default*: `border-radius: 1rem; padding: 12px 24px; font-weight: 600;`.
    *   *Hover*: Aumento sutil de la elevación o ligero oscurecimiento del fondo.
    *   *Disabled*: Opacidad al 40% y cursor no permitido.
*   **Entradas de Datos (Inputs)**: Borde de 1px color *Outline* (#6F797A). Al ganar foco, anillo de 2px color *Primary*.
*   **Tarjetas (Cards)**: Fondo blanco, radio de 1.5rem y sombra `ambient-shadow`: `0 10px 40px -10px rgba(6, 105, 114, 0.08)`.

### Iconografía
Se utilizan los **Material Symbols Outlined**.
*   **Grosor**: 200 - 400 (Variable según el contexto).
*   **Estilo**: Siempre lineales (Outlined) para mantener la ligereza visual.

---

## 6. Espaciado y Cuadrícula
Aura utiliza un sistema basado en múltiplos de **8px** para garantizar la armonía proporcional.

*   **Unidad Base**: 8px.
*   **Márgenes de Página**: 32px (Desktop) / 16px (Mobile).
*   **Gutter (Canal):** 24px entre tarjetas o columnas.
*   **Stacking Gap**: 16px entre elementos de un mismo grupo (ej. título y párrafo).

---

## 7. Tono y Voz
La comunicación de Aura debe reflejar sus valores fundamentales en cada mensaje.

*   **Adjetivos**: Profesional, Empático, Directo, Tranquilizador.
*   **Ejemplos de Aplicación**:
    *   ❌ **Incorrecto**: "Error en el sistema. Vuelva a intentarlo." (Frío y alarmante).
    *   ✅ **Correcto**: "No hemos podido completar la acción en este momento. Por favor, inténtalo de nuevo en unos segundos." (Tranquilizador y explicativo).
    *   ❌ **Incorrecto**: "¿Borrar reporte?" (Directo pero agresivo).
    *   ✅ **Correcto**: "¿Estás seguro de que deseas eliminar este reporte? Esta acción no se puede deshacer." (Protector y empático).
