import { usePage } from "@inertiajs/react";

export default function Students() {
    const { name, father } = usePage().props;
    return (
        <div>
            <h1>Students</h1>
            <ul>
                <li>Name {name}</li>
                <li>Father {father}</li>
            </ul>
        </div>
    );
}
