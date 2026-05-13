#!/usr/bin/env python3
"""
Generate a maskable PWA icon from the existing icon-512x512.png.
Maskable icons need the important content within the inner 80% (safe zone).
We scale the icon down to 80% and center it on a background that matches
the icon's edge color (the gradient edge).
"""
from PIL import Image, ImageDraw
import os

ICONS_DIR = os.path.join(os.path.dirname(__file__), '..', 'public', 'assets', 'images', 'icons')

# Source: the beautiful gradient circle icon (actually 1024x1024)
src_path = os.path.join(ICONS_DIR, 'icon-512x512.png')
src = Image.open(src_path).convert('RGBA')
original_size = src.size[0]  # 1024

# Target maskable size: 512x512 (standard for manifest)
target_size = 512

# Sample the background color from the corners of the source image to get the
# dominant bg color (should be white/near-white since the icon is a circle on white)
# For the maskable icon, we'll use a matching gradient background color
# The icon's gradient goes from teal (top-left) to purple (bottom-right)
# A good solid fill is a mid-point of the gradient
bg_color = (240, 245, 245, 255)  # Light near-white to match the existing icon background

# Create the canvas
canvas = Image.new('RGBA', (target_size, target_size), bg_color)

# Scale the source icon to fit in the safe zone (80% of target)
safe_zone = int(target_size * 0.80)
icon_resized = src.resize((safe_zone, safe_zone), Image.LANCZOS)

# Center the icon on the canvas
offset = (target_size - safe_zone) // 2
canvas.paste(icon_resized, (offset, offset), icon_resized)

# Save as PNG
out_path = os.path.join(ICONS_DIR, 'icon-maskable-512x512.png')
canvas.save(out_path, 'PNG', optimize=True)
print(f"✅ Maskable icon saved to: {out_path}")
print(f"   Size: {target_size}x{target_size}")
print(f"   Safe zone icon: {safe_zone}x{safe_zone} centered with {offset}px padding")

# Also regenerate icon-192x192.png from the same source to ensure consistency
icon_192 = src.resize((192, 192), Image.LANCZOS)
out_192 = os.path.join(ICONS_DIR, 'icon-192x192.png')
icon_192.save(out_192, 'PNG', optimize=True)
print(f"✅ icon-192x192.png regenerated from same source")

# And the apple-touch-icon at 180x180
icon_180 = src.resize((180, 180), Image.LANCZOS)
out_180 = os.path.join(ICONS_DIR, 'apple-touch-icon.png')
icon_180.save(out_180, 'PNG', optimize=True)
print(f"✅ apple-touch-icon.png regenerated (180x180)")
