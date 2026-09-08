# Prisma — Design System

> **Objetivo:** guía visual y de experiencia para mantener consistencia en toda la app de Prisma.  
> **Marca:** tecnología para cuidar la convivencia escolar.  
> **Principio rector:** **ver antes para cuidar mejor**.

---

## 1. Esencia de marca

Prisma convierte información compleja sobre convivencia escolar en señales claras, accionables y humanas.

La experiencia debe sentirse:

- **Humana:** personas reales en el centro.
- **Clara:** información fácil de leer y comprender.
- **Confiable:** rigor, consistencia y transparencia.
- **Protectora:** anticipa riesgos sin generar alarma.
- **Pensada para escuelas:** lenguaje y flujos adaptados a la realidad educativa.

### Promesa de experiencia

Cada pantalla debe ayudar a responder rápidamente:

1. **¿Qué está pasando?**
2. **¿A quién afecta?**
3. **¿Qué prioridad tiene?**
4. **¿Qué puedo hacer ahora?**

---

## 2. Principios de diseño

### 2.1 Perspectiva
Mostrar múltiples miradas antes de concluir.

- Priorizar contexto sobre métricas aisladas.
- Combinar señales cuantitativas y cualitativas.
- Evitar juicios definitivos cuando los datos son insuficientes.

### 2.2 Claridad
La información importante debe entenderse en segundos.

- Una idea principal por bloque.
- Jerarquía visual evidente.
- Etiquetas cortas y específicas.
- Datos con contexto y significado.

### 2.3 Protección
La interfaz acompaña, no dramatiza.

- Alertas proporcionales al nivel de riesgo.
- Lenguaje neutral y orientado a la acción.
- Evitar iconografía agresiva o alarmista.

### 2.4 Comunidad
La app representa una red: estudiantes, equipos, familias y centro educativo.

- Favorecer vistas de grupo además de vistas individuales.
- Mostrar relaciones, evolución y contexto.
- Diseñar para colaboración entre roles.

---

## 3. Paleta de color

### Colores principales

| Token | Hex | Uso recomendado |
|---|---|---|
| `--prisma-mint` | `#79E0CC` | Prevención, mejora, éxito, acciones positivas |
| `--prisma-lavender` | `#C9A7FF` | Comunidad, vínculos, sociometría, acentos |
| `--prisma-sky` | `#A6E4FF` | Información, claridad, datos y orientación |
| `--prisma-cloud` | `#F4F7FD` | Fondos, superficies suaves, secciones |
| `--prisma-charcoal` | `#1E2433` | Texto principal, navegación, alto contraste |

### Neutros de interfaz

```css
--white: #FFFFFF;
--gray-50: #F8FAFC;
--gray-100: #F1F5F9;
--gray-200: #E2E8F0;
--gray-300: #CBD5E1;
--gray-500: #64748B;
--gray-700: #334155;
--gray-900: #0F172A;
```

### Colores semánticos

Los colores semánticos deben ser suaves y siempre acompañados de texto o icono.

```css
--success: #2FBF9F;
--success-bg: #EAFBF6;

--info: #62B9F3;
--info-bg: #EDF8FF;

--warning: #F2B84B;
--warning-bg: #FFF8E7;

--danger: #E86B73;
--danger-bg: #FFF0F1;
```

> No usar rojo como color dominante de la experiencia. Las señales de riesgo deben ser visibles sin resultar punitivas.

---

## 4. Gradiente de marca

El gradiente principal conecta **perspectiva + claridad + protección**.

```css
--prisma-gradient: linear-gradient(
  90deg,
  #79E0CC 0%,
  #A6E4FF 45%,
  #C9A7FF 100%
);
```

### Uso

✅ Permitido en:

- Logo y elementos de marca.
- Indicadores de progreso.
- Líneas decorativas.
- Highlights.
- Cabeceras especiales.
- Visualizaciones no críticas.

❌ Evitar en:

- Texto largo.
- Tablas.
- Fondos completos de dashboards.
- Estados de error o alerta.

---

## 5. Tipografía

### Principal — Manrope

Usar para:

- Titulares
- Navegación
- Labels
- Botones
- Métricas principales
- Mensajes clave

```css
font-family: "Manrope", sans-serif;
```

### Apoyo — Inter

Usar para:

- Texto largo
- Tablas
- Formularios
- Descripciones
- Ayuda contextual

```css
font-family: "Inter", sans-serif;
```

### Escala tipográfica

| Estilo | Tamaño | Peso | Line-height |
|---|---:|---:|---:|
| Display | 40px | 700 | 1.1 |
| H1 | 32px | 700 | 1.2 |
| H2 | 24px | 700 | 1.25 |
| H3 | 20px | 650 | 1.3 |
| Body L | 18px | 400 | 1.55 |
| Body | 16px | 400 | 1.55 |
| Body S | 14px | 400 | 1.5 |
| Label | 13px | 600 | 1.3 |
| Caption | 12px | 500 | 1.4 |

### Reglas

- No usar más de 3 pesos tipográficos en una misma vista.
- Evitar MAYÚSCULAS en textos largos.
- Limitar el ancho de lectura a `65–75ch`.
- Usar cifras tabulares en dashboards cuando sea posible.

---

## 6. Espaciado

Base de **4px**.

```txt
4   = xs
8   = sm
12  = md
16  = lg
24  = xl
32  = 2xl
48  = 3xl
64  = 4xl
```

### Reglas

- Padding mínimo de card: `16px`.
- Padding recomendado de card desktop: `20–24px`.
- Separación entre secciones: `32–48px`.
- Separación entre título y contenido: `8–12px`.
- Targets interactivos: mínimo `44x44px`.

---

## 7. Bordes y radios

Prisma debe sentirse suave, moderna y segura.

```css
--radius-sm: 8px;
--radius-md: 12px;
--radius-lg: 16px;
--radius-xl: 20px;
--radius-pill: 999px;
```

### Uso

- Inputs y botones: `10–12px`.
- Cards: `14–16px`.
- Modales: `16–20px`.
- Pills y estados: `999px`.

---

## 8. Sombras

Sombras ligeras, nunca dramáticas.

```css
--shadow-sm: 0 1px 2px rgba(30, 36, 51, 0.06);
--shadow-md: 0 6px 18px rgba(30, 36, 51, 0.08);
--shadow-lg: 0 14px 36px rgba(30, 36, 51, 0.10);
```

Priorizar bordes suaves sobre sombras cuando sea posible.

```css
border: 1px solid #E2E8F0;
```

---

## 9. Layout

### Desktop

- Sidebar fija o plegable.
- Área principal centrada.
- Máximo recomendado de contenido: `1440px`.
- Grid base: 12 columnas.
- Gap: `24px`.

### Tablet

- Sidebar colapsable.
- Grid de 6 columnas.
- Cards adaptables a 2 columnas.

### Mobile

- Navegación inferior o drawer.
- Una columna.
- Evitar tablas horizontales cuando exista alternativa.
- Priorizar resumen + detalle progresivo.

---

## 10. Navegación principal

Arquitectura recomendada:

```txt
Inicio
Estudiantes
Convivencia
Reportes
Protocolos
Comunidad
Configuración
```

### Módulos de producto

#### Prisma Canal
**Detección temprana y reporte seguro**

Color principal: Mint.

#### Prisma Sociometría
**Clima escolar y vínculos**

Color principal: Lavender.

#### Prisma Protocolos
**Gestión y seguimiento de casos**

Color principal: Sky / Blue.

#### Prisma Centro
**Visión integral del colegio**

Color principal: Lavender profundo / mezcla de marca.

---

## 11. Cards

### Card estándar

```txt
┌─────────────────────────────┐
│ Eyebrow / estado            │
│ Título                      │
│ Descripción breve           │
│                             │
│ Métrica / contenido         │
│                             │
│ Acción secundaria      →    │
└─────────────────────────────┘
```

### Estilo

```css
background: #FFFFFF;
border: 1px solid #E2E8F0;
border-radius: 16px;
padding: 20px;
```

### Card de métrica

Debe incluir:

- Label
- Valor principal
- Variación
- Periodo
- Tooltip de definición cuando la métrica pueda ser ambigua

Ejemplo:

```txt
Clima escolar
8.2
↑ +12% respecto al trimestre anterior
```

---

## 12. Botones

### Primario

```css
background: #1E2433;
color: #FFFFFF;
border-radius: 10px;
```

Uso: acción principal única de la vista.

### Secundario

```css
background: #FFFFFF;
color: #1E2433;
border: 1px solid #CBD5E1;
```

### Tercario / Ghost

Sin contenedor, para acciones de baja prioridad.

### Acción positiva

Puede usar `--prisma-mint` si representa avanzar, completar o acompañar.

### Reglas

- Un solo CTA primario visible por bloque.
- Verbo explícito: `Crear protocolo`, `Ver estudiante`, `Enviar seguimiento`.
- Evitar botones genéricos como `Aceptar` cuando pueda ser más específico.

---

## 13. Inputs y formularios

```css
height: 44px;
border: 1px solid #CBD5E1;
border-radius: 10px;
padding-inline: 12px;
background: #FFFFFF;
```

### Focus

```css
outline: 3px solid rgba(166, 228, 255, 0.45);
border-color: #62B9F3;
```

### Validación

- Mostrar el error junto al campo.
- Explicar cómo resolverlo.
- No depender solo del color.
- Conservar los valores ya introducidos.

---

## 14. Estados y badges

### Formato

```txt
● Normal
● Atención
● Prioritario
● En seguimiento
● Resuelto
```

### Reglas

Cada estado debe tener:

- Texto
- Icono o forma
- Color
- Tooltip si el significado no es obvio

Nunca usar únicamente `verde / amarillo / rojo` como sistema de interpretación.

---

## 15. Alertas y señales de riesgo

La prioridad es **informar y orientar**, no asustar.

### Anatomía

```txt
[Icono] Señal que requiere atención
Descripción breve con contexto.

Siguiente acción recomendada
[Revisar caso]
```

### Tono recomendado

✅ `Se han detectado cambios recientes en el patrón de convivencia.`  
✅ `Conviene revisar esta señal con el equipo responsable.`

❌ `PELIGRO`  
❌ `CASO GRAVE`  
❌ `ALERTA CRÍTICA` salvo que exista un protocolo formal que requiera esa categoría.

---

## 16. Data visualization

### Objetivo

Las gráficas deben ayudar a detectar patrones, no decorar.

### Reglas

- Usar máximo 4–5 colores por gráfica.
- Mantener los significados de color constantes.
- Mostrar labels o valores cuando sean importantes.
- Incluir comparativa temporal si aporta contexto.
- Añadir descripción textual para accesibilidad.
- Evitar 3D.
- Evitar donut/pie con más de 4 categorías.

### Línea de tendencia

Color recomendado:

```css
#6F8CFF
```

### Áreas positivas

Usar Mint.

### Relaciones / comunidad

Usar Lavender.

---

## 17. Iconografía

Estilo:

- Lineal.
- Geométrico.
- Redondeado.
- Trazos simples.
- Grosor consistente.

Tamaño:

```txt
16px → inline
20px → controles
24px → navegación
32px+ → cards de módulo
```

Los iconos nunca sustituyen un label en acciones críticas.

---

## 18. Fotografía

### Dirección de arte

- Entornos escolares reales.
- Luz natural.
- Situaciones auténticas.
- Diversidad e inclusión.
- Momentos de convivencia, orientación y colaboración.
- Composición limpia y cercana.

### Evitar

- Estética de banco de imágenes evidente.
- Dramatización del bullying.
- Personas aisladas usadas como recurso de miedo.
- Imágenes excesivamente corporativas o de “wellness”.
- Filtros saturados.

---

## 19. Motion

Movimiento discreto y funcional.

```txt
Microinteracciones: 120–180ms
Transiciones:      180–240ms
Paneles/modales:   220–300ms
```

Curva recomendada:

```css
cubic-bezier(0.2, 0.8, 0.2, 1);
```

Animar:

- Aparición de estados.
- Cambio de filtros.
- Expansión de detalles.
- Confirmaciones.

Evitar animaciones continuas.

Respetar `prefers-reduced-motion`.

---

## 20. Voz y tono

### Personalidad

- Cercana
- Serena
- Clara
- Profesional
- Orientada a acompañar

### Microcopy

✅ `Revisar señales`  
✅ `Ver evolución`  
✅ `Añadir seguimiento`  
✅ `No hay señales nuevas por ahora`  
✅ `Los datos se actualizaron hace 5 min`

❌ `Explorar insights disruptivos`  
❌ `No se encontraron incidencias problemáticas`  
❌ `Todo está perfecto`

### Sobre personas

Hablar de **personas y situaciones**, no etiquetar identidades.

✅ `Estudiante con señales de aislamiento`  
❌ `Estudiante problemático`

---

## 21. Accesibilidad

Objetivo mínimo: **WCAG 2.2 AA**.

### Checklist

- Contraste mínimo `4.5:1` para texto normal.
- Contraste `3:1` para texto grande.
- Navegación completa por teclado.
- Focus visible.
- Labels asociados a inputs.
- `aria-live` para cambios dinámicos importantes.
- No depender solo del color.
- Texto alternativo para imágenes informativas.
- Targets táctiles ≥ `44px`.
- Tablas con headers correctamente asociados.
- Orden de lectura coherente.

---

## 22. Privacidad y confianza

Dado que Prisma trabaja con información sensible de convivencia escolar:

- Mostrar solo la información necesaria para cada rol.
- Evitar exposición innecesaria de datos personales.
- Confirmar acciones destructivas o sensibles.
- Registrar cambios relevantes.
- Mostrar claramente estados de sincronización y actualización.
- Diferenciar hechos, señales e interpretaciones.
- Evitar rankings públicos de estudiantes o grupos.
- No gamificar situaciones de riesgo.

---

## 23. Dashboard de inicio

### Orden recomendado

1. **Resumen del centro**
2. **Señales que requieren atención**
3. **Evolución de convivencia**
4. **Casos o seguimientos activos**
5. **Actividad reciente**
6. **Acciones recomendadas**

Ejemplo:

```txt
Buenos días, Laura

Resumen de convivencia
┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│ Clima  8.2   │ │ Casos  12    │ │ Señales  8   │
│ ↑ +12%       │ │ ↓ -25%       │ │ revisar      │
└──────────────┘ └──────────────┘ └──────────────┘

Tendencia de convivencia
[ gráfico ]

Áreas de atención
Respeto y trato        ━━━━━━━░░
Inclusión              ━━━━━━░░░
Bienestar emocional    ━━━━━━━━░
```

---

## 24. Sistema de componentes

Componentes base mínimos:

```txt
Avatar
Badge
Button
Card
Checkbox
Combobox
Dialog
Drawer
Dropdown
EmptyState
FilterBar
Input
MetricCard
Notification
Pagination
Progress
Radio
Search
Select
Sidebar
Skeleton
Stat
Stepper
Switch
Table
Tabs
Tag
Textarea
Toast
Tooltip
TrendChart
```

Componentes específicos Prisma:

```txt
RiskSignalCard
StudentSummary
ClimateScore
SociometryMap
ProtocolStatus
CaseTimeline
FollowUpCard
CommunityIndicator
SchoolOverview
ActionRecommendation
ConfidentialityBadge
```

---

## 25. Empty states

Un estado vacío debe explicar:

1. Qué significa.
2. Si es normal.
3. Qué puede hacer la persona.

Ejemplo:

```txt
No hay señales nuevas

No se han detectado cambios relevantes en este periodo.

[Ver histórico]
```

---

## 26. Loading states

Preferir **skeletons** sobre spinners para contenido estructurado.

- Cards → skeleton de card.
- Tablas → filas skeleton.
- Métricas → bloque de número.
- Carga > 5 segundos → añadir mensaje.

---

## 27. Responsive behavior

### Breakpoints sugeridos

```css
--bp-sm: 640px;
--bp-md: 768px;
--bp-lg: 1024px;
--bp-xl: 1280px;
--bp-2xl: 1536px;
```

### Prioridades en mobile

1. Alertas y acciones.
2. Resumen.
3. Tendencias.
4. Detalle.
5. Configuración avanzada.

---

## 28. CSS tokens de referencia

```css
:root {
  /* Brand */
  --prisma-mint: #79E0CC;
  --prisma-lavender: #C9A7FF;
  --prisma-sky: #A6E4FF;
  --prisma-cloud: #F4F7FD;
  --prisma-charcoal: #1E2433;

  /* Semantic */
  --success: #2FBF9F;
  --info: #62B9F3;
  --warning: #F2B84B;
  --danger: #E86B73;

  /* Background */
  --bg-app: #F7F9FD;
  --bg-surface: #FFFFFF;
  --bg-subtle: #F4F7FD;

  /* Text */
  --text-primary: #1E2433;
  --text-secondary: #64748B;
  --text-muted: #94A3B8;
  --text-on-dark: #FFFFFF;

  /* Border */
  --border-subtle: #E2E8F0;
  --border-strong: #CBD5E1;

  /* Radius */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;

  /* Shadow */
  --shadow-sm: 0 1px 2px rgba(30, 36, 51, 0.06);
  --shadow-md: 0 6px 18px rgba(30, 36, 51, 0.08);

  /* Motion */
  --ease-prisma: cubic-bezier(0.2, 0.8, 0.2, 1);
}
```

---

## 29. Tailwind — referencia opcional

```js
colors: {
  prisma: {
    mint: "#79E0CC",
    lavender: "#C9A7FF",
    sky: "#A6E4FF",
    cloud: "#F4F7FD",
    charcoal: "#1E2433",
  }
}
```

```js
fontFamily: {
  display: ["Manrope", "sans-serif"],
  sans: ["Inter", "sans-serif"],
}
```

```js
borderRadius: {
  prisma: "16px",
}
```

---

## 30. Reglas para nuevas pantallas

Antes de aprobar una pantalla, validar:

- [ ] ¿La acción principal se entiende en menos de 5 segundos?
- [ ] ¿La jerarquía visual refleja la prioridad real?
- [ ] ¿Los datos tienen contexto?
- [ ] ¿Las señales de riesgo son proporcionales y no alarmistas?
- [ ] ¿El lenguaje respeta a estudiantes y comunidad?
- [ ] ¿Hay una siguiente acción clara?
- [ ] ¿Funciona con teclado?
- [ ] ¿Funciona en mobile?
- [ ] ¿No depende solo del color?
- [ ] ¿Respeta los permisos y privacidad del rol?
- [ ] ¿Usa componentes existentes antes de crear nuevos?
- [ ] ¿Se mantiene la estética clara, humana y protectora de Prisma?

---

## 31. Referencia visual resumida

```txt
PRISMA
Tecnología para cuidar la convivencia escolar

Visual:
  limpio + luminoso + humano
  blanco + cloud
  charcoal para estructura
  mint / sky / lavender para significado y marca

UI:
  cards suaves
  radios amplios
  poco ruido visual
  datos claros
  iconografía lineal
  fotografías reales
  gradientes solo como acento

Experiencia:
  prevenir
  comprender
  actuar
```

---

**Última regla:** si una decisión visual hace que Prisma parezca más “tecnológica” pero menos humana, elegir la opción más humana siempre que no perjudique claridad, accesibilidad o seguridad.
