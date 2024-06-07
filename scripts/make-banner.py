import random

WHITE = (255, 255, 255)
GREEN = (25, 135, 84)
RED = (220, 53, 69)


def mix(x: float, v1: int, v2: int) -> int:
    return round(v1 + x * (v2 - v1))


def mix3(
    x: float, v1: tuple[int, int, int], v2: tuple[int, int, int]
) -> tuple[int, int, int]:
    return mix(x, v1[0], v2[0]), mix(x, v1[1], v2[1]), mix(x, v1[2], v2[2])


with open("banner.svg", mode="w") as f:
    f.write("""
<svg viewBox="0 0 64 32" width="640" height="320" xmlns="http://www.w3.org/2000/svg">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900');

        * {
            font: bold 10px 'Roboto';
        }
    </style>
    <rect width="64" height="32" fill="#eee" />

    <text x="3.5" y="9" fill="rgb(194, 24, 91)">Habit</text>
    <text x="27.5" y="9" fill="rgb(236, 64, 122)">Mosaic</text>
""")

    for x in range(20):
        for y in range(7):
            v = random.randint(-100, 100) / 100

            color = mix3(-v, WHITE, RED) if v < 0 else mix3(v, WHITE, GREEN)

            f.write(
                f'<rect fill="rgb{color}" x="{2.5 + x * 3}" y="{10 + y * 3}" width="2.5" height="2.5" />\n'
            )

    f.write('</svg>')
