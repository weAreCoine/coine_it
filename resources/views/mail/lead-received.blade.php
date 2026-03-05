<div>
    <p>Un nuovo lead è stato generato da Coiné. I suoi dati sono qui sotto.</p>
    <ul>
        <li>
            <p>Nome: {{$lead->name}}</p>
        </li>
        <li>
            <p>Email: {{$lead->email}}</p>
        </li>
        <li>
            <p>Telefono: {{$lead->phone??''}}</p>
        </li>
        <li>
            <p>Website: {{$lead->website??''}}</p>
        </li>
        <li>
            <p>Services: {{is_array($lead->services) ? implode(', ', $lead->services) : $lead->services}}</p>
        </li>
        <li>
            <p>Project: {{$lead->project ?? ''}}</p>
        </li>
        <li>
            <p>Budget: {{$lead->budget ?? ''}}</p>
        </li>

    </ul>

</div>
