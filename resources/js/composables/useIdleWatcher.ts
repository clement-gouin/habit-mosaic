import { onMounted, ref } from 'vue';

export default function useIdleWatcher (onIdleStop: (time: number) => void, threshold = 60): void {
    const lastIdle = ref<number | undefined>(undefined);

    function idleStop (): void {
        const now = Date.now();
        const delta = now - (lastIdle.value ?? now);
        if (delta > threshold * 1000) {
            onIdleStop(delta);
        }
        lastIdle.value = now;
    }

    onMounted(() => {
        window.onload = idleStop;
        window.onfocus = idleStop;
        window.onmousemove = idleStop;
        window.onmousedown = idleStop; // touchscreen presses
        window.ontouchstart = idleStop;
        window.onclick = idleStop; // touchpad clicks
        window.onkeydown = idleStop; // onkeypress is deprectaed
        window.addEventListener('scroll', idleStop, true); // improved; see comments
    });
}
