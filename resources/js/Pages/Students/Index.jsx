import { Link } from "@inertiajs/react";

export default function Students({ users }) {
    const students = users.data;

    return (
        <div>
            <h1>Students</h1>
            <ul>
                {students.map((user) => (
                    <Link href={route("students.show", user.id)}>
                        {user.name}
                    </Link>
                ))}
            </ul>
        </div>
    );
}
