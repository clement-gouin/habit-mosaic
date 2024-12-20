<template>
    <canvas :id="id" />
</template>

<script setup lang="ts">
import Chart, { ChartItem, ChartOptions, ChartData, ChartConfiguration } from 'chart.js/auto';
import { onMounted, ref, watch } from 'vue';

interface Props {
    data: ChartData
    options?: ChartOptions
}

const props = defineProps<Props>();

const id = ref<string>('graph-' + Math.random().toString(36)
    .substring(2));

let chart: Chart|null = null;

function makeChart () {
    if (chart) {
        try {
            chart.destroy();
        } catch (error) {
            // ignore
        }
    }
    const ctx = document.getElementById(id.value);
    if (ctx !== null) {
        chart = new Chart(
            ctx as ChartItem,
            {
                data: props.data,
                options: props.options ?? {},
                plugins: [
                    {
                        id: 'background',
                        beforeDraw: (chart, args, options) => {
                            if (options.color) {
                                const { ctx } = chart;
                                ctx.save();
                                ctx.globalCompositeOperation = 'destination-over';
                                ctx.fillStyle = options.color;
                                ctx.fillRect(0, 0, chart.width, chart.height);
                                ctx.restore();
                            }
                        },
                        afterDraw: (chart, args, options) => {
                            const { ctx } = chart;
                            const textHeight = chart.height * (options.textSize ?? 0.2);

                            ctx.fillStyle = '#21252933';
                            ctx.textAlign = 'left';

                            let iconText = '';
                            let iconWidth = 0;

                            if (options.icon) {
                                iconText = window.getComputedStyle(document.querySelector(`.fa-${options.icon}`), ':before').getPropertyValue('content')
                                    .replace(/"/g, '');

                                ctx.font = `${textHeight}px FontAwesome`;
                                iconWidth = ctx.measureText(iconText).width;
                            }

                            let textWidth = 0;

                            if (options.text) {
                                ctx.font = `${textHeight}px Roboto`;
                                textWidth = ctx.measureText(options.text).width;
                            }

                            const startX = (chart.width - iconWidth - textWidth) / 2;

                            if (options.icon) {
                                ctx.font = `${textHeight}px FontAwesome`;
                                ctx.fillText(iconText, startX, chart.height / 2);
                            }

                            if (options.text) {
                                ctx.font = `${textHeight}px Roboto`;
                                ctx.fillText(options.text, startX + iconWidth, chart.height / 2);
                            }

                            ctx.restore();
                        }
                    }
                ]
            } as ChartConfiguration
        );
    }
}

onMounted(makeChart);
watch(() => props.options, makeChart);
watch(() => props.data, makeChart);
</script>
