export async function createAlert (type: string, message: string, duration = 1): Promise<boolean> {
    const id = 'alert-' + String(Math.random()).substring(2);
    let resolvePromise: (value: boolean) => void;

    document.getElementById('alert-container')?.insertAdjacentHTML('afterbegin', `
        <div id="${id}" class="alert alert-${type} alert-dismissible fade" role="alert">
            <div>${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    setTimeout(() => {
        document.getElementById(id)?.classList.add('show');
        setTimeout(() => {
            document.getElementById(id)?.classList.remove('show');
            resolvePromise(true);
            setTimeout(() => {
                document.getElementById(id)?.remove();
            }, 500);
        }, duration * 1000);
    });

    return await new Promise(resolve => { resolvePromise = resolve; });
}
