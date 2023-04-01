const { useEffect, useState, useRef, Fragment } = React;

function App(){
    const refForm = useRef();
	const [ employees, setEmployees ] = useState(null);
    const [ activeEmployee, setActiveEmployee ] = useState(null);

    useEffect(()=>{
        fetch('/employees.php', {
            method: 'GET',
        }).then(async response => setEmployees(await response.json()));
    }, []);

    function sendForm(){
        const body = new FormData(refForm.current);

        fetch('/employees.php', {
            method: 'POST',
            body,
        }).then(async response => !response.ok && alert(await response.text()));
    }

    function selectEmployee(id){
        fetch('/employees.php?id='+id, {
            method: 'GET',
        }).then(async response => setActiveEmployee(await response.json()));
    }

	return (
		<Fragment>
            {activeEmployee ? (
                <ActiveEmployee sendForm={sendForm} employee={activeEmployee}>
                    <FormWithValues refForm={refForm} employee={activeEmployee} />
                </ActiveEmployee>
            ) : (
                <Fragment>
                    <header>
                        <FormWithoutValues refForm={refForm} />
                        <button onClick={sendForm}>Добавить</button>
                    </header>
                    {employees ? (
                        <Employees employees={employees} selectEmployee={selectEmployee} />
                    ) : (
                        <div>
                            Загрузка...
                        </div>
                    )}
                </Fragment>
            )}
		</Fragment>
	);
}

function FormWithValues({refForm, employee: [ employee ]}){
    return (
        <form ref={refForm}>
            <input name="name" defaultValue={employee.name} type="text" placeholder="Имя" />
            <input name="surname" defaultValue={employee.surname} type="text" placeholder="Фамилия" />
            <input name="job" defaultValue={employee.job} type="text" placeholder="Должность" />
            <input name="email" defaultValue={employee.email} type="text" placeholder="Email" />
            <input name="phone_number" defaultValue={employee['phone_number']} type="text" placeholder="Домашний телефон" />
            <input name="notes" defaultValue={employee.notes} type="text" placeholder="Заметки" />
        </form>
    )
}
function FormWithoutValues({refForm}){
    return (
        <form ref={refForm}>
            <input name="name" type="text" placeholder="Имя" />
            <input name="surname" type="text" placeholder="Фамилия" />
            <input name="job" type="text" placeholder="Должность" />
            <input name="email" type="text" placeholder="Email" />
            <input name="phone_number" type="text" placeholder="Домашний телефон" />
            <input name="notes" type="text" placeholder="Заметки" />
        </form>
    )
}

function ActiveEmployee({children}){
    return (
        <Fragment>
            {children}
            <button>Изменить</button>
        </Fragment>
    )
}

function Employees({employees, selectEmployee}){
    return (
        <ul>
            {employees.map(({id, name, surname}) => <li key={id} onClick={()=>selectEmployee(id)}>
                Имя и фамилия: {name} {surname}
            </li>)}
        </ul>
    )
}

const root = ReactDOM.createRoot(document.getElementById("app"));

root.render(
	<App />
);